import socket
import sys

def receive(client_connection):
    request_data = b''
    while True:
      new_data = client_connection.recv(4098)
      if (len(new_data) == 0):
        # client disconnected
        return None, None
      request_data += new_data
      if b'\r\n\r\n' in request_data:
        break

    parts = request_data.split(b'\r\n\r\n', 1)
    header = parts[0]
    body = parts[1]

    if b'Content-Length' in header:
      headers = header.split(b'\r\n')
      for h in headers:
        if h.startswith(b'Content-Length'):
          blen = int(h.split(b' ')[1]);
          break
    else:
        blen = 0

    while len(body) < blen:
      body += client_connection.recv(4098)

    print(header.decode('utf-8', 'replace'), flush=True)
    print('')
    print(body.decode('utf-8', 'replace'), flush=True)

    return header, body

HOST = sys.argv[1]
PORT = int(sys.argv[2])
ROOT = sys.argv[3]

fileNotFound = """\
HTTP/1.1 404 Not Found
Connection: close

<html>
<body>
<h2>
404 Page Not Found!
</h2>
</body>
</html>
""".encode('UTF-8')

listen_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
listen_socket.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
listen_socket.bind((HOST, PORT))
listen_socket.listen(1)
print(f'Serving HTTP on port {PORT} ...')

while True:
  client_connection, client_address = listen_socket.accept()
  header, body = receive(client_connection)

  if header is None or body is None:
    client_connection.close()
    continue

  headerArray = header.split(b'\n')
  startLine = headerArray[0].decode('UTF-8')
  resourcePath = startLine.split(' ')[1]
  UAString = ""

  for line in headerArray:
    if b"User-Agent" in line:
      UAString = line.decode('UTF-8')

  if "Firefox" in UAString:
    http_response = """\
HTTP/1.1 400 Bad Request
Connection: close

<html>
<body>
<h1>400 Bad Request!</h1>
<h2>You're using Firefox, which is not secure! Switch your browser to access this server!</h2>
""".encode('UTF-8')
    client_connection.sendall(http_response)
    client_connection.close()

  else:
    

    if 'jpg' in startLine:
      http_response = """\
HTTP/1.1 200 OK
Content-Type: image/jpeg
Connection: close

"""
      http_response = http_response.replace('\n','\r\n').encode('UTF-8')

      try:
        with open(ROOT + resourcePath, 'rb') as fh:
          http_response += fh.read()
      except FileNotFoundError:
        http_response = fileNotFound


    elif 'png' in startLine:
      http_response = """\
HTTP/1.1 200 OK
Content-Type: image/png
Connection: close

"""
      http_response = http_response.replace('\n','\r\n').encode('UTF-8')

      try:
        with open(ROOT + resourcePath, 'rb') as fh:
          http_response += fh.read()
      except FileNotFoundError:
        http_response = fileNotFound


    elif 'html' in startLine:
      http_response = """\
HTTP/1.1 200 OK
Content-Type: text/html
Connection: close

"""
      http_response = http_response.replace('\n','\r\n').encode('UTF-8')

      try:
        with open(ROOT + resourcePath, 'rb') as fh:
          http_response += fh.read()
      except FileNotFoundError:
        http_response = fileNotFound


    else:
      http_response = """\
HTTP/1.1 200 OK
Content-Type: text/html; charset=UTF-8
Connection: close

""" 
      http_response = http_response.replace('\n','\r\n').encode('UTF-8')
      http_response += """
<html>
<body>
<h1>This is my Root Directory Landing page!</h1>
</body>
</html>
""".encode('UTF-8')
        

    client_connection.sendall(http_response)
    client_connection.close()