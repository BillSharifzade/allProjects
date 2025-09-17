const express = require('express');
const app = express();
const basicAuth = require('express-basic-auth');
const PORT = 8080;

const songs = [
  "The Summer Wind",
  "My Kind of Town",
  "That's Life",
  "All or Nothing at All",
  "You Do Something to Me",
  "I’ve Got the World on a String",
  "Love and Marriage",
  "It’s Nice to Go Trav’ling",
  "The Lady Is a Tramp",
  "Night and Day",
  "Pennies from Heaven",
  "I’ve Got You Under My Skin",
  "You Make Me Feel So Young",
  "Strangers in the Night",
  "Fly Me to the Moon",
  "Come Fly with Me",
  "Witchcraft",
  "The Way You Look Tonight",
  "New York, New York",
  "Something Stupid"
];

app.get('/', (req, res) => {
  const randomSong = songs[Math.floor(Math.random() * songs.length)];
  res.send(randomSong);
});

app.get('/birth_date', (req, res) => {
  res.send('December 12, 1915');
});

app.get('/birth_city', (req, res) => {
  res.send('Hoboken, New Jersey');
});

app.get('/wives', (req, res) => {
  res.send('Nancy Barbato, Ava Gardner, Mia Farrow, Barbara Marx');
});

app.get('/picture', (req, res) => {
  res.redirect('https://en.wikipedia.org/wiki/Frank_Sinatra#/media/File:Frank_Sinatra2,_Pal_Joey.jpg');
});

app.get('/public', (req, res) => {
  res.send('Everybody can see this page');
});

app.use('/protected', basicAuth({
  users: { 'admin': 'admin' },
  challenge: true,
  realm: 'Restricted Area',
  unauthorizedResponse: 'Not authorized' 
}));

app.get('/protected', (req, res) => {
  res.send('Welcome, authenticated client');
});

app.listen(PORT, '0.0.0.0', () => {
  console.log(`Server is running on http://0.0.0.0:${PORT}`);
});
