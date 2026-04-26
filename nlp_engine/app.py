from fastapi import FastAPI
from pydantic import BaseModel
import pickle
import numpy as np
import random

app = FastAPI(title="Sipaduhok NLP Engine")

print("Loading model NLP...")
try:
    with open('chatbot_model.pkl', 'rb') as f:
        model = pickle.load(f)
    with open('responses_dict.pkl', 'rb') as f:
        responses_dict = pickle.load(f)
    print(f"Model berhasil dimuat! ({len(responses_dict)} intent)")
except Exception as e:
    print(f"Error loading model: {e}")

class ChatRequest(BaseModel):
    teks: str
    role: str

@app.post("/chat")
async def chat_endpoint(req: ChatRequest):
    # Prediksi murni natural language tanpa injeksi role
    text_input = req.teks.lower()
    probs = model.predict_proba([text_input])[0]
    
    # Ambil top 5 probabilitas
    top5_indices = np.argsort(probs)[::-1][:5]
    
    predicted_tag = model.classes_[top5_indices[0]]
    max_prob = probs[top5_indices[0]]
    
    # OVERLAP RESOLVER: Cari intent di top 5 yang sesuai dengan role user
    # Jika jarak skor dengan top 1 sangat tipis (overlap), kita pilih intent milik user
    for i in top5_indices:
        tag = model.classes_[i]
        score = probs[i]
        intent_data_temp = responses_dict.get(tag)
        role = intent_data_temp.get('role', 'global') if intent_data_temp else 'global'
        
        # Jika selisih skor > 0.04 dari yang terbaik, berarti intent sudah terlalu meleset, hentikan pencarian
        if (max_prob - score) > 0.04:
            break
            
        if role == req.role or role == 'global':
            predicted_tag = tag
            max_prob = score
            break
            
    # DEBUG: Menampilkan hasil tebakan di terminal uvicorn
    print(f"-> User: {req.teks} | Role: {req.role} | Prediksi Final: {predicted_tag} | Skor: {max_prob:.4f}")

    # GUARDRAIL 1: Threshold sangat rendah (0.5%) karena 131 kelas
    if max_prob < 0.005 or predicted_tag == "out_of_scope":
        return {
            "response": "Maaf, saya hanya diprogram untuk memberikan informasi dan panduan teknis terkait penggunaan sistem informasi SIPADUHOK. Silakan tanyakan hal seputar menu dan fitur.",
            "tag": "out_of_scope",
            "confidence": float(max_prob)
        }

    intent_data = responses_dict.get(predicted_tag)
    
    if not intent_data:
        return {
            "response": "Maaf, saya belum memiliki informasi untuk topik tersebut.",
            "tag": predicted_tag,
            "confidence": float(max_prob)
        }
    
    # GUARDRAIL 2: Pengecekan Otorisasi Role
    # 'global' = semua role boleh akses
    # Jika tag diawali nama role tertentu, tetap tampilkan info tapi beri catatan
    intent_role = intent_data.get('role', 'global')
    
    if intent_role != 'global' and intent_role != req.role:
        balasan = random.choice(intent_data['responses'])
        nama_menu = predicted_tag
        if isinstance(balasan, dict) and 'ui' in balasan and 'title' in balasan['ui']:
            nama_menu = balasan['ui']['title']
        else:
            nama_menu = nama_menu.replace('admin_', '').replace('siswa_', '').replace('guru_', '').replace('_', ' ').title()
            
        role_target = intent_role.replace('_', ' ').title()
        role_sekarang = req.role.replace('_', ' ').title()
        
        penolakan = f"Anda mencoba mengakses fitur/informasi mengenai **{nama_menu}**.\n\n"
        penolakan += f"Menu tersebut merupakan otoritas khusus untuk role **{role_target}**. Saat ini Anda masuk ke sistem sebagai **{role_sekarang}**.\n\n"
        penolakan += "Demi keamanan data dan mencegah error (403 Forbidden), tautan navigasi ke menu tersebut dinonaktifkan."
        
        return {
            "response": penolakan,
            "ui_data": None,
            "tag": predicted_tag,
            "confidence": float(max_prob),
            "role_mismatch": True
        }
        
    balasan = random.choice(intent_data['responses'])
    ui_data = None
    
    if isinstance(balasan, dict):
        teks_balasan = balasan.get('text', '')
        ui_data = balasan.get('ui', None)
    else:
        teks_balasan = balasan
        
    teks_balasan = teks_balasan.replace('â†’', '->').replace('→', '->')
    if ui_data and 'steps' in ui_data:
        ui_data['steps'] = [step.replace('â†’', '->').replace('→', '->') for step in ui_data['steps']]
    
    return {
        "response": teks_balasan,
        "ui_data": ui_data,
        "tag": predicted_tag,
        "confidence": float(max_prob)
    }