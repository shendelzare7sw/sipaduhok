import requests
import json

url = "http://127.0.0.1:5000/chat"

# Data simulasi request dari Laravel
data = {
    "teks": "cara kumpul tugas gimana min?",
    "role": "siswa"
}

print(f"Mengirim pesan: '{data['teks']}' (Role: {data['role']})")

try:
    response = requests.post(url, json=data)
    print("\n--- RESPON DARI BOT ---")
    print(json.dumps(response.json(), indent=4))
except Exception as e:
    print("Gagal terhubung ke server:", e)