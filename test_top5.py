import pickle
import numpy as np

with open('c:\\laragon\\www\\sipaduhok\\nlp_engine\\chatbot_model.pkl', 'rb') as f:
    model = pickle.load(f)
with open('c:\\laragon\\www\\sipaduhok\\nlp_engine\\responses_dict.pkl', 'rb') as f:
    responses_dict = pickle.load(f)

texts = [
    "cara kerjakan tugas",
    "buat pengumuman"
]

req_role = "admin"

for text in texts:
    probs = model.predict_proba([text])[0]
    top5_indices = np.argsort(probs)[::-1][:5]
    print(f"\nQuery: '{text}'")
    for i in top5_indices:
        tag = model.classes_[i]
        role = responses_dict[tag]['role']
        score = probs[i]
        print(f"  - {tag} ({role}): {score:.4f}")
