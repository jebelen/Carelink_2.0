import os

# --- Shared Configuration for the Python API ---

# Image dimensions your model was trained with
IMG_HEIGHT, IMG_WIDTH = 128, 128

# List of class names in the order your model predicts them
# This order MUST match the alphabetical order of the subdirectories in `training_data`
CLASS_NAMES = ['Fake_PWD_ID', 'Fake_Senior_ID', 'Not_An_ID', 'PWD_ID', 'Senior_ID']

# Path to your trained Keras/TensorFlow model file
# Construct an absolute path to the model file to avoid CWD issues
MODEL_PATH = os.path.join(os.path.dirname(__file__), 'pasig_id_verifier_model.h5')

# --- Training Specific Configuration ---
BATCH_SIZE = 32
EPOCHS = 10
DATA_DIR = 'training_data'
