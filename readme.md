Hello!

#How does it work
- import schema.sql
- import data_patch.sql 
- login by doing post request to /api/login with body {"username": "test", "password": "test"}
- copy token from response and add X-AUTH-TOKEN header with token value
- Do CRUD requests for managing your assets

#Additional info
- Currency rates being cached for 1 min, since it's not trading platform, we don't really need live currency rate
- Probably went a bit overboard with the task, so went easy on testing, made just couple