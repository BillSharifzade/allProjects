import mysql.connector

db = mysql.connector.connect(
    host="localhost",
    user="root",       
    password="root",       
    database="clash_royale_prototype",
    port = "8889"
)
cursor = db.cursor()

cursor.execute("DROP TABLE IF EXISTS characters")
cursor.execute("""
CREATE TABLE IF NOT EXISTS characters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    hp INT NOT NULL,
    damage INT NOT NULL
)
""")

characters = [
    ("Knight", 100, 15),
    ("Archer", 80, 20),
    ("Giant", 150, 10),
    ("Wizard", 90, 25),
    ("Dragon", 120, 30),
    ("Goblin", 70, 18),
    ("Prince", 110, 22),
    ("Skeleton", 60, 10),
    ("Barbarian", 100, 20),
    ("Minion", 80, 12)
]

cursor.executemany("INSERT INTO characters (name, hp, damage) VALUES (%s, %s, %s)", characters)
db.commit()

cursor.execute("SELECT * FROM characters")
all_characters = cursor.fetchall()

def display_characters():
    print("\nAvailable Characters:")
    for char in all_characters:
        print(f"{char[0]}: {char[1]} (HP: {char[2]}, Damage: {char[3]})")

def choose_character(player_number):
    while True:
        try:
            char_id = int(input(f"\nPlayer {player_number}, choose a character by ID: "))
            cursor.execute("SELECT * FROM characters WHERE id = %s", (char_id,))
            chosen_char = cursor.fetchone()
            if chosen_char:
                return chosen_char
            else:
                print("Invalid character ID. Try again!")
        except ValueError:
            print("Please enter a valid number!")

def battle(player1, player2):
    print(f"\nBattle Start: {player1[1]} (HP: {player1[2]}, Damage: {player1[3]}) vs {player2[1]} (HP: {player2[2]}, Damage: {player2[3]})")
    while player1[2] > 0 and player2[2] > 0:
        player2 = (player2[0], player2[1], player2[2] - player1[3], player2[3])
        if player2[2] <= 0:
            print(f"\n{player1[1]} wins!")
            break
        player1 = (player1[0], player1[1], player1[2] - player2[3], player1[3])
        if player1[2] <= 0:
            print(f"\n{player2[1]} wins!")
            break

def main():
    print("Welcome to the Clash Royale Prototype!")
    display_characters()
    player1 = choose_character(1)
    player2 = choose_character(2)
    battle(player1, player2)

if __name__ == "__main__":
    main()