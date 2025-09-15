import whisper

model = whisper.load_model("base")
transcriptions = {}

for video in ["video1.mp4", "video2.mp4"]:
    result = model.transcribe(video)
    transcriptions[video] = result["text"]

with open("transcribtion.bash", "w") as f:
    for video, text in transcriptions.items():
        f.write(f"{video}:\n{text}\n\n")