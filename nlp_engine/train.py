import json
import pickle
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
from sklearn.pipeline import Pipeline

print("Memulai proses re-training model NLP lokal...")

# 1. Menggunakan path dari agenmu yang benar
with open('../dataset/intents.json', 'r', encoding='utf-8-sig') as f:
    data = json.load(f)

patterns, tags = [], []
responses_dict = {}

# 2. Ekstraksi data
print("Ekstraksi data dari intents.json...")
for intent in data['intents']:
    tag = intent['tag']
    responses_dict[tag] = {
        'role': intent['role'],
        'responses': intent['responses']
    }
    for pattern in intent['patterns']:
        # Hapus injeksi role manual agar model bisa belajar natural language murni
        patterns.append(pattern)
        tags.append(tag)

# 3. Tetap gunakan Logistic Regression + Ngram (Rekomendasiku agar skornya normal)
print(f"Melatih model NLP dengan {len(patterns)} sampel pola dari {len(responses_dict)} intent...")
model_pipeline = Pipeline([
    ('tfidf', TfidfVectorizer(ngram_range=(1, 2))),
    ('clf', LogisticRegression(random_state=42, max_iter=1000))
])

model_pipeline.fit(patterns, tags)

# 4. Simpan model ke dalam folder nlp_engine
print("Menyimpan model ke chatbot_model.pkl dan responses_dict.pkl...")
with open('chatbot_model.pkl', 'wb') as f:
    pickle.dump(model_pipeline, f)

with open('responses_dict.pkl', 'wb') as f:
    pickle.dump(responses_dict, f)

print("Training Selesai! Model Logistic Regression baru telah siap.")