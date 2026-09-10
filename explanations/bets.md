classDiagram
direction BT
class bets {
   timestamp date_bet
   int price_bet
   int user_id
   int lot_id
   int id
}
class categories {
   varchar(128) character_code
   varchar(128) name_category
   int id
}
class lots {
   timestamp date_creation
   varchar(255) title
   text lot_description
   varchar(255) img
   int start_price
   date date_finish
   int step
   int user_id
   int winner_id
   int category_id
   int id
}
class users {
   timestamp date_registration
   varchar(128) email
   varchar(128) user_name
   text user_password
   text contacts
   int id
}

bets  -->  lots : lot_id:id
bets  -->  users : user_id:id
lots  -->  categories : category_id:id
lots  -->  users : user_id:id
lots  -->  users : winner_id:id
