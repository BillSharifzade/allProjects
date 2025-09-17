from flask import Flask, redirect
import random
from functools import wraps
from flask import request, Response

app = Flask(__name__)

songs = [
frank_sinatra_songs = [
    "Angel Eyes",
    "I've Got the World on a String",
    "You Do Something to Me",
    "The Lady is a Tramp",
    "Witchcraft",
    "One for My Baby (and One More for the Road)",
    "Young at Heart",
    "Fly Me to the Moon",
    "Moonlight Serenade",
    "All or Nothing at All",
    "Mack the Knife",
    "It Was a Very Good Year",
    "The Best Is Yet to Come",
    "Nice ‘n’ Easy",
    "That's Life",
    "I’ve Got You Under My Skin",
    "Somethin’ Stupid",
    "Strangers in the Night",
    "Come Fly with Me",
    "Summer Wind"
]

]

birth_date = "December 12, 1915"
birth_city = "Hoboken, New Jersey"
wives = ["Nancy Barbato", "Ava Gardner", "Mia Farrow", "Barbara Marx"]
picture_url = "https://en.wikipedia.org/wiki/Frank_Sinatra#/media/File:Frank_Sinatra2,_Pal_Joey.jpg"

def check_auth(username, password):
    return username == 'admin' and password == 'admin'

def authenticate():
    return Response(
    'Not authorized', 401,
    {'WWW-Authenticate': 'Basic realm="Restricted Area"'})

def requires_auth(f):
    @wraps(f)
    def decorated_function(*args, **kwargs):
        auth = request.authorization
        if not auth or not check_auth(auth.username, auth.password):
            return authenticate()
        return f(*args, **kwargs)
    return decorated_function

@app.route('/')
def random_song():
    return random.choice(songs)

@app.route('/birth_date')
def get_birth_date():
    return birth_date

@app.route('/birth_city')
def get_birth_city():
    return birth_city

@app.route('/wives')
def get_wives():
    return ', '.join(wives)

@app.route('/picture')
def get_picture():
    return redirect(picture_url)

@app.route('/public')
def public_page():
    return "Everybody can see this page"

@app.route('/protected')
@requires_auth
def protected_page():
    return "Welcome, zadrot anonymus"

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8080)