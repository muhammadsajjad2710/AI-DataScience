

function createPerson(){
    var person= new Object();
    person.name ="Gauri";
    person.designation="Trainer";
    person.phno=592002224;
    return person;
}

var gauri=createPerson();

function createPerson2(){
    var person= {};
    person.name ="Gauri2";
    person.designation="Trainer";
    person.phno=592002224;
    return person;
}

var gauri=createPerson2();

function createPerson3(){
    var person= {};
    person['name'] ="Gauri3";
    person['designation']="Trainer";
    person['phno']=592002224;
    return person;
}

var gauri=createPerson3();
alert("name:"+gauri.name+"designation:"+gauri.designation+"phno:"+gauri.phno);

function createPerson4(){
    var person= {
                    name:"Gauri4",
                    age:60,
                    designation:"Trainer",
                    phno:6352352523

            };
   
    return person;
}

var gauri=createPerson4();

function Person(){

    this.name="Gauri5";
    this.designation="Trainer";
    this.age=90;
}
gauri = new Person();

var Animal={
    type:"Invertibrates",
    displayType: function(){ alert("type is:")}
}

var Horse= Object.create(Animal);
Horse.type="something else";

var Trainer={
        name:"ABC",
        subjects:["Math","Physics","Chem"],
        teaches:["F","S"],
        age:60
}
var properties ="";
for(p in Trainer)
{
    properties+= p +" : "
}

console.log("before deletion: " +properties);
delete Trainer.age;
properties =""
;for(p in Trainer)
{
    properties+= p +" : "
}


console.log("after deletion:" +properties);