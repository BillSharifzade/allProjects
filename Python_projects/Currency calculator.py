def somoni_to_other_currencies(user_input1, user_input2):
    currency = {"dollar" : 10.80,
                "euro" : 12.6,    
                "ruble" : 0.12,
                "pound" : 13.2,
                "yuan": 1.57}
#You can add other currencies as well   
    if user_input1 in currency:
       return user_input2 * currency[user_input1]
    else:
        print("Choose one of this choices : dollar, euro, ruble, pound")

user_input1 = input("Choose the currency: ")
user_input2 = int(input("Enter amount of funds: "))
print (somoni_to_other_currencies(user_input1, user_input2),"somoni") 