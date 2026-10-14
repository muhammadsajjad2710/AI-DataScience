// this is a comment

// arithimatic operators

/*
console.log(2-1);
var diff=3-2;
console.log("diff :" +diff);
diff++;
console.log("diff :" +diff);
var product = 7*9;
console.log("product :" +product);
var div = 5/2;
console.log("div :" +div);
var mod = 5%4;
console.log("mod :" +mod);
*/

// assigment operators

/*
var s=9;
s+=8; // s=s+8
console.log(s)

var s=9;
s-=8; // s=s+8
console.log(s)
*/

// comparision operators
/*
var s=9;
var s2="78";
if(s>=s2){
    console.log('if part');
}
else{
    console.log('else part');

}
*/
// boolean operator
/*
var s=9;
var s2="9";
if(!(s==s2) || s===s2){
    console.log('if part');
}
else{
    console.log('else part');

}
*/

// ternary operator

 // console.log(4>2 ?true:false)
/* Switch

 var weight=parseFloat(prompt('enter your weight!!'));
 switch(weight){

    case 1:document.write("wegiht is 1");
        //break;
    case 2:document.write("weight is 2");
        break;
    case 3:document.write("weight is 3");
        break;
    default:document.write("wegiht is not in 1,2,3");
 }
*/

// for loops 
/*
var subjects=['maths','physics','english'];
var marks = new Array();
var num1;

for ( var j=0;j<subjects.length;j++){
    num1= parseFloat(prompt("enter marks for subject "+ subjects[j]));
    marks[j]=num1;


}

for ( m in marks){
    console.log(marks[m]);
}
*/

// while loop 

var i=8;
while(i<10){
    console.log(i);
    i++;
}

do{

console.log(i);
}while(i<10)

