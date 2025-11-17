import tensorflow as tf
from tensorflow.keras import layers, models
import os

# --- Configuration ---
IMG_HEIGHT, IMG_WIDTH = 128, 128 # Must match app.py
BATCH_SIZE = 32
EPOCHS = 10 # You might need to adjust this based on your dataset size and model performance
DATA_DIR = 'training_data' # Relative path to your training data
MODEL_SAVE_PATH = 'pasig_id_verifier_model.h5' # Must match app.py
CLASS_NAMES = ['Fake_PWD_ID', 'Fake_Senior_ID', 'Not_An_ID', 'PWD_ID', 'Senior_ID'] # Must match app.py and your folder names

def create_model(num_classes):
    """Defines a simple CNN model."""
    model = models.Sequential([
        layers.Conv2D(32, (3, 3), activation='relu', input_shape=(IMG_HEIGHT, IMG_WIDTH, 3)),
        layers.MaxPooling2D((2, 2)),
        layers.Conv2D(64, (3, 3), activation='relu'),
        layers.MaxPooling2D((2, 2)),
        layers.Conv2D(128, (3, 3), activation='relu'),
        layers.MaxPooling2D((2, 2)),
        layers.Flatten(),
        layers.Dense(128, activation='relu'),
        layers.Dense(num_classes, activation='softmax') # Softmax for multi-class classification
    ])
    return model

def train_model():
    print("Loading training data...")
    # Load data from directories
    # This function automatically infers labels from subdirectory names
    train_ds = tf.keras.preprocessing.image_dataset_from_directory(
        DATA_DIR,
        labels='inferred',
        label_mode='categorical', # Use categorical for one-hot encoded labels
        image_size=(IMG_HEIGHT, IMG_WIDTH),
        interpolation='nearest',
        batch_size=BATCH_SIZE,
        shuffle=True,
        seed=42 # for reproducibility
    )

    # Get class names from the dataset to verify against expected CLASS_NAMES
    found_class_names = train_ds.class_names
    print(f"Found classes in data directory: {found_class_names}")

    # It's crucial that the order of classes in train_ds.class_names matches CLASS_NAMES
    # image_dataset_from_directory sorts class names alphabetically by default.
    # Ensure your CLASS_NAMES list is also sorted alphabetically if your folder names are.
    if sorted(found_class_names) != sorted(CLASS_NAMES):
        print("WARNING: Class names found in data directory do not match expected CLASS_NAMES.")
        print(f"Expected: {CLASS_NAMES}, Found: {found_class_names}")
        print("Please ensure your folder names match the CLASS_NAMES in app.py and train_model.py, and are sorted consistently.")
        # You might want to exit or raise an error here if this mismatch is critical
        return

    num_classes = len(CLASS_NAMES)

    print(f"Creating model with {num_classes} classes...")
    model = create_model(num_classes)

    model.compile(optimizer='adam',
                  loss='categorical_crossentropy',
                  metrics=['accuracy'])

    model.summary()

    print("Starting model training...")
    history = model.fit(
        train_ds,
        epochs=EPOCHS
    )

    print(f"Saving model to {MODEL_SAVE_PATH}...")
    model.save(MODEL_SAVE_PATH)
    print("Model training complete and saved.")

if __name__ == '__main__':
    # Ensure the training_data directory exists and has subfolders
    expected_subdirs = [os.path.join(DATA_DIR, name) for name in CLASS_NAMES]
    all_exist = True
    for subdir in expected_subdirs:
        if not os.path.exists(subdir):
            print(f"Error: Expected subdirectory '{subdir}' not found.")
            all_exist = False
    
    if not all_exist:
        print("\nPlease create the following subdirectories inside 'training_data':")
        for name in CLASS_NAMES:
            print(f"- {DATA_DIR}/{name}")
        print("And place your images accordingly before running this script.")
    else:
        train_model()
