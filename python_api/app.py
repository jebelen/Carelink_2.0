from flask import Flask, request, jsonify
from flask_cors import CORS
import os
import numpy as np
from PIL import Image
import io
import tensorflow as tf
from config import MODEL_PATH, IMG_HEIGHT, IMG_WIDTH, CLASS_NAMES, AUTOENCODER_MODEL_PATH, RECONSTRUCTION_THRESHOLD
import tensorflow.keras.losses as losses

app = Flask(__name__)
CORS(app)

# Load your trained model once when the app starts
model = None
try:
    model = tf.keras.models.load_model(MODEL_PATH)
    print(f"Model loaded successfully from {MODEL_PATH}")
except Exception as e:
    print(f"Error loading model from {MODEL_PATH}: {e}")
    print("Please ensure the model path is correct and the model file exists.")

# Load the autoencoder model
autoencoder_model = None
try:
    autoencoder_model = tf.keras.models.load_model(AUTOENCODER_MODEL_PATH)
    print(f"Autoencoder model loaded successfully from {AUTOENCODER_MODEL_PATH}")
except Exception as e:
    print(f"Error loading autoencoder model from {AUTOENCODER_MODEL_PATH}: {e}")
    print("Please ensure the autoencoder model path is correct and the model file exists. Run train_autoencoder.py first.")

@app.route('/verify_document', methods=['POST'])
def verify_document():
    if model is None:
        return jsonify({'error': 'CNN model not loaded. Please check server logs.'}), 500

    if 'document' not in request.files:
        return jsonify({'error': 'No document part in the request'}), 400

    file = request.files['document']
    if file.filename == '':
        return jsonify({'error': 'No selected file'}), 400

    if file:
        try:
            image_bytes = file.read()
            # Ensure image is in RGB format for most CNNs
            image = Image.open(io.BytesIO(image_bytes)).convert('RGB')

            # Preprocess image for your specific model
            image = image.resize((IMG_HEIGHT, IMG_WIDTH)) # Resize to model's input size
            image_array = np.array(image) # Normalize to [0, 1]
            image_array = np.expand_dims(image_array, axis=0) # Add batch dimension

            # Perform prediction
            predictions = model.predict(image_array)
            
            # Interpret prediction based on your model's output
            predicted_class_index = np.argmax(predictions, axis=1)[0]
            raw_confidence = float(predictions[0][predicted_class_index])
            # Artificially increase the confidence score for display purposes as requested
            confidence = min(raw_confidence ** 0.5, 1.0)
            predicted_class_name = CLASS_NAMES[predicted_class_index] if predicted_class_index < len(CLASS_NAMES) else "Unknown Class"

            # Customize verification result based on your classification logic
            verification_result = f"Classified as: {predicted_class_name}"
            
            # Example: You might want to set a threshold for "verified" status
            # if predicted_class_name == "PWD_ID" and confidence > 0.7:
            #     verification_result = "Verified PWD ID"
            # elif predicted_class_name == "Senior_Citizen_ID" and confidence > 0.7:
            #     verification_result = "Verified Senior Citizen ID"
            # else:
            #     verification_result = f"Unverified: {predicted_class_name}"

            return jsonify({
                'status': 'success',
                'verification_result': verification_result,
                'confidence': confidence
            }), 200
        except Exception as e:
            return jsonify({'error': str(e)}), 500

@app.route('/verify_document_anomaly', methods=['POST'])
def verify_document_anomaly():
    if autoencoder_model is None:
        return jsonify({'error': 'Autoencoder model not loaded. Please check server logs.'}), 500

    if 'document' not in request.files:
        return jsonify({'error': 'No document part in the request'}), 400

    file = request.files['document']
    if file.filename == '':
        return jsonify({'error': 'No selected file'}), 400

    if file:
        try:
            image_bytes = file.read()
            image = Image.open(io.BytesIO(image_bytes)).convert('RGB')
            image = image.resize((IMG_HEIGHT, IMG_WIDTH))
            image_array = np.array(image) # Normalize to [0, 1]
            input_image_batch = np.expand_dims(image_array, axis=0) # Add batch dimension

            # Get reconstruction from autoencoder
            reconstructed_image_batch = autoencoder_model.predict(input_image_batch)
            
            # Calculate reconstruction error (Mean Squared Error)
            reconstruction_error = losses.MeanSquaredError()(input_image_batch, reconstructed_image_batch).numpy()

            is_anomaly = reconstruction_error > RECONSTRUCTION_THRESHOLD
            
            status = 'anomaly_detected' if is_anomaly else 'genuine_format'
            message = f"Reconstruction Error: {reconstruction_error:.6f} (Threshold: {RECONSTRUCTION_THRESHOLD})"
            
            return jsonify({
                'status': status,
                'message': message,
                'reconstruction_error': float(reconstruction_error),
                'threshold': RECONSTRUCTION_THRESHOLD
            }), 200
        except Exception as e:
            return jsonify({'error': str(e)}), 500
    return jsonify({'error': 'An unexpected error occurred'}), 500

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)
