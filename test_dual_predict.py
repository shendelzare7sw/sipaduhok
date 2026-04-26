import pickle
import numpy as np

with open('c:\\laragon\\www\\sipaduhok\\nlp_engine\\chatbot_model.pkl', 'rb') as f:
    model = pickle.load(f)

texts = [
    "role_admin cara kerjakan tugas dari guru?",
    "cara kerjakan tugas dari guru?"
]

for text in texts:
    probs = model.predict_proba([text])[0]
    max_prob = np.max(probs)
    predicted_tag = model.classes_[np.argmax(probs)]
    print(f"'{text}' -> {predicted_tag} (Score: {max_prob:.4f})")
