# Codeigniter-3-Model-Builder
Create model classes from all tables

Database:
MySQL Database

Installation :
1. Make connection to your database using config file ({base_path}/config/database.php)
2. Copy BuildModel.php to {base_path}/application/controller/
3. Call controller using {base_url)/index.php/buildmodel/
4. Select table
5. new model file will be automatically created in the folder {base_path}/model/{table_name}.php

Classes :
1. $model->field  // contain single field array
2. $model->fields //contain record array
3. $model->select($keys) // select record (return true or false)
4. $model->insert() // insert record (return empty string or error message)
5. $model->update() // update record (return empty string or error message)
6. $model->delete() // delete record (return empty string or error message)
7. $model->execute($parameter) // $paramter = "insert", or  "update", or "delete"
