import numpy as np

def load_dataset():
    with np.load("mnist.npz") as f:
        x_train, y_train = f['x_train'], f['y_train']
        x_test, y_test = f['x_test'], f['y_test']
    
    # Normalize to [-0.5, 0.5] for better learning stability
    x_train = (x_train.astype("float32") / 255) - 0.5
    x_test = (x_test.astype("float32") / 255) - 0.5
    
    # Reshape into (num_samples, 784)
    x_train = x_train.reshape(x_train.shape[0], -1)
    x_test = x_test.reshape(x_test.shape[0], -1)
    
    # One-hot encode labels
    y_train = np.eye(10)[y_train]
    y_test = np.eye(10)[y_test]

    return x_train, y_train, x_test, y_test