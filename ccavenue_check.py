import json
import requests
import psycopg2
from datetime import datetime
from Crypto.Cipher import AES
from Crypto.Hash import MD5
from Crypto.Util.Padding import pad, unpad
import base64
import urllib.parse
import logging

# =============================
# LOGGING
# =============================
logging.basicConfig(
    filename="ccavenue_cron.log",
    level=logging.INFO,
    format="%(asctime)s - %(levelname)s - %(message)s"
)

# =============================
# CONFIG
# =============================
WORKING_KEY = "615717775ADFA47A79451673555A2642"
ACCESS_CODE = "AVDY79MF57BN32YDNB"

DB_CONFIG = {
    "host": "10.10.100.12",
    "user": "postgres",
    "password": "postgres",
    "dbname": "ssusdb",
    "port": 5432
}

CCA_URL = "https://api.ccavenue.com/apis/servlet/DoWebTrans"


# =============================
# CRYPTO FUNCTIONS
# =============================
def get_key(working_key):
    m = MD5.new()
    m.update(working_key.encode('utf-8'))
    return m.digest()


def encrypt(data, working_key):
    key = get_key(working_key)
    iv = bytes(range(16))
    cipher = AES.new(key, AES.MODE_CBC, iv)
    encrypted = cipher.encrypt(pad(data.encode('utf-8'), 16))
    return base64.b64encode(encrypted).decode('utf-8')


def decrypt(enc_data, working_key):
    key = get_key(working_key)
    iv = bytes(range(16))

    # Fix base64 padding
    enc_data += '=' * (-len(enc_data) % 4)

    cipher = AES.new(key, AES.MODE_CBC, iv)
    decrypted = cipher.decrypt(base64.b64decode(enc_data))
    return unpad(decrypted, 16).decode('utf-8')


# =============================
# CHECK ORDER STATUS
# =============================
def check_order(order_id, cursor, conn):
    try:
        payload = {"order_no": order_id}
        request_data = json.dumps(payload)
        enc_request = encrypt(request_data, WORKING_KEY)

        params = {
            "enc_request": enc_request,
            "access_code": ACCESS_CODE,
            "request_type": "JSON",
            "response_type": "JSON",
            "command": "orderStatusTracker",
            "version": "1.2"
        }

        # Correct POST request (form data)
        response = requests.post(
            CCA_URL,
            data=params,
            timeout=30
        )

        body = response.text
        print("Raw API response:", body)

        parsed = urllib.parse.parse_qs(body)

        if "enc_response" not in parsed:
            msg = f"API error for order {order_id}: {body}"
            print(msg)
            logging.error(msg)
            return

        enc_response = parsed["enc_response"][0]
        enc_response = urllib.parse.unquote(enc_response)

        decrypted = decrypt(enc_response, WORKING_KEY)
        data = json.loads(decrypted)

        if "order_status" in data:
            sql = """
                UPDATE tbz_ccavenue_txns
                SET order_status = %s,
                    bank_ref_no = %s,
                    status_message = %s,
                    updated_at = %s
                WHERE order_id = %s
            """

            values = (
                data.get("order_status"),
                data.get("order_bank_ref_no"),
                data.get("order_bank_response"),
                datetime.now(),
                data.get("order_no")
            )

            cursor.execute(sql, values)
            conn.commit()

            msg = f"Order {data['order_no']} updated → {data['order_status']}"
            print(msg)
            logging.info(msg)

        else:
            msg = f"Order {order_id} returned no status."
            print(msg)
            logging.warning(msg)

    except Exception as e:
        msg = f"Error processing order {order_id}: {str(e)}"
        print(msg)
        logging.error(msg)


# =============================
# MAIN CRON FUNCTION
# =============================
def run_cron():
    try:
        print("Connecting to database...")
        conn = psycopg2.connect(**DB_CONFIG)
        cursor = conn.cursor()
        print("Database connected successfully.")

        cursor.execute("""
            SELECT order_id
            FROM tbz_ccavenue_txns
            WHERE order_status IS NULL
            OR order_status NOT IN ('Shipped','Success','Failed')
            LIMIT 1
        """)

        orders = cursor.fetchall()
        print(f"{len(orders)} pending orders found.")

        if not orders:
            print("No pending orders to check.")
            cursor.close()
            conn.close()
            return

        for row in orders:
            order_id = row[0]
            print(f"Checking order: {order_id}")
            check_order(order_id, cursor, conn)

        cursor.close()
        conn.close()
        print("Cron run completed.")

    except Exception as e:
        msg = f"Database or cron error: {str(e)}"
        print(msg)
        logging.error(msg)


if __name__ == "__main__":
    run_cron()
