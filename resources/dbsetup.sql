Create Table Users(
	id int
    	Unsigned
    	Primary Key
    	Auto_Increment
    	Not Null,
    username varchar(24)
		Unique
    	Not Null,
    email varchar(256)
		Unique
    	Not Null,
    password varchar(255)
    	Not Null,
    registrationTime int
		Unsigned
		Default Unix_Timestamp()
		Not Null
);