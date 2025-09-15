import numpy as np
import matplotlib.pyplot as plt
import utils

def relu(x):
    return np.maximum(0, x)
def relu_derivative(x):
    return (x > 0).astype(float)
def softmax(x):
    exp_x = np.exp(x - np.max(x))
    return exp_x / np.sum(exp_x, axis=0, keepdims=True)
images, labels, test_images, test_labels = utils.load_dataset()

layers = [784, 128, 64, 10] 
weights = [np.random.randn(layers[i], layers[i-1]) * 0.01 for i in range(1, len(layers))]
biases = [np.zeros((layers[i], 1)) for i in range(1, len(layers))]
epochs = 2
learning_rate = 0.006
e_loss, e_correct = [], []

for epoch in range(epochs):
    loss, correct = 0, 0
    for image, label in zip(images, labels):
        image, label = image.reshape(-1, 1), label.reshape(-1, 1)
        activations, zs = [image], []
        for W, b in zip(weights, biases):
            zs.append(W @ activations[-1] + b)
            activations.append(relu(zs[-1]) if len(activations) < len(weights) else softmax(zs[-1]))
        
        loss += -np.sum(label * np.log(activations[-1] + 1e-9))
        correct += int(np.argmax(activations[-1]) == np.argmax(label))
        
        delta = activations[-1] - label
        gradients_W, gradients_b = [], []
        
        for i in range(len(weights) - 1, -1, -1):
            gradients_W.insert(0, delta @ activations[i].T)
            gradients_b.insert(0, delta)
            if i > 0:
                delta = (weights[i].T @ delta) * relu_derivative(zs[i-1])
        for i in range(len(weights)):
            weights[i] -= learning_rate * gradients_W[i]
            biases[i] -= learning_rate * gradients_b[i]
    
    e_loss.append(loss / images.shape[0])
    e_correct.append(correct / images.shape[0])
    print(f"Epoch {epoch + 1}: Loss = {e_loss[-1]:.4f}, Accuracy = {e_correct[-1] * 100:.2f}%")

plt.figure(figsize=(12, 4))
plt.subplot(1, 2, 1)
plt.plot(e_loss, label="Loss")
plt.xlabel("Epochs")
plt.ylabel("Loss")
plt.legend()
plt.subplot(1, 2, 2)
plt.plot(e_correct, label="Accuracy", color='green')
plt.xlabel("Epochs")
plt.ylabel("Accuracy")
plt.legend()
plt.show()

test_image = plt.imread("custom.jpg", format="jpeg")
test_image = 1 - (np.dot(test_image[..., :3], [0.299, 0.587, 0.114]) / 255)
test_image = test_image.reshape(-1, 1)

activation = test_image
for W, b in zip(weights, biases):
    activation = relu(W @ activation + b) if W.shape[0] != 10 else softmax(W @ activation + b)

plt.imshow(test_image.reshape(28, 28), cmap="Greys")
plt.title(f"Billy thinks its: {np.argmax(activation)}")
plt.show()