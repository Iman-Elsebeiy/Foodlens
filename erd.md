## user 
- user_id
- name
- email
- password
- phone 
- age
- gender
- wight
- height
- timestamps 


## user_goal
- user_goal_id
- current_weight //recommendation of the current_weight
- target_weight
- calorie_goal   // total calories that should be have per day
- water_goal    // total water in your body in %
- current_water  // current water in your body in %
- sleep_goal    // total sleep that should be have
- current_sleep  `ask?`
- user_id fk(user.user_id) // relation type : one to one
- timestamps 

## scan_history
- scan_history_id
- total_food_items
- calories_consumed // total calories that should be have per item
- user_id fk(user.user_id) // relation type : many to one
- food_id fk(food.food_id) // relation type : many to one
- daily_data_id fk(daily_data.daily_data_id) // relation type : many to one
- timestamps 

## daily_data
- daily_data_id
- calories_consumed // total calories that should be have per day
- user_id fk(user.user_id) // relation type : many to one
- water // total water you drink this day
- sleep // total sleep you sleep this day
- weight // total weight you lose this day
- timestamps 




## food
- food_id
- food_name => model_output.predictions.class
- unit_weight
- calories_per_100g




## model_output (classification)
```json
{
  "predictions": [
    {
      "x": 400,
      "y": 299,
      "width": 724,
      "height": 456,
      "confidence": 0.83,
      "class": "dark-chocolate",
      "class_id": 168,
      "detection_id": "92b29b5f-f4c1-4ec8-8d74-91c08d84e777"
    }
  ]
}
```


## ask client
- user_goal table
- condition on the user goal if i want to gain weight or lose weight or maintain weight what is the condition 
