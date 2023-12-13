import mysql.connector
from pymongo import MongoClient

# Kết nối MongoDB
client = MongoClient('mongodb://localhost:27017/')
db = client['sssTutter_db']
collection = db['images']

# Kết nối MySQL
mysql_conn = mysql.connector.connect(
    host='localhost',
    user='root',
    password='622001y',
    database='ssstutter_db',
    port="3306"
)
mysql_cursor = mysql_conn.cursor(dictionary=True)

# Thực hiện truy vấn SQL
mysql_cursor.execute('SELECT * FROM image')
rows = mysql_cursor.fetchall()

# Import dữ liệu vào MongoDB
for row in rows:
    collection.insert_one(row)

# Đóng kết nối
mysql_cursor.close()
mysql_conn.close()
