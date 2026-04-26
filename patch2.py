import json

filepath = r'c:\laragon\www\sipaduhok\dataset\intents.json'
with open(filepath, 'r', encoding='utf-8-sig') as f:
    data = json.load(f)

for intent in data['intents']:
    if intent['tag'] == 'siswa_pembayaran':
        if "untuk lihat menu pembayaran" not in intent['patterns']:
            intent['patterns'].append("untuk lihat menu pembayaran")
    elif intent['tag'] == 'bendahara_pembayaran':
        if "untuk lihat menu pembayaran" not in intent['patterns']:
            intent['patterns'].append("untuk lihat menu pembayaran")

with open(filepath, 'w', encoding='utf-8') as f:
    json.dump(data, f, indent=4, ensure_ascii=False)
