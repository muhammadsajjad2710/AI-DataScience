// named function
// first step is function declaration 
function addNum(a,b){
    console.log("function addNum is called");

    return a+b;
}

// function calling 
//addNum(2,6);
//console.log(addNum(3,5));
//var sum = addNum(2,3);
//console.log(sum);
//addNum(2,3);

var anon=function (a,b){
    console.log("anonymous function  is called");

    return a+b;
}
anon(23+2);

setTimeout(function(){console.log('anonymous function call in setTi')},3000);

// using constructors 
var cons=new Function("a","b","console.log('in constructors function');return a+b;");
console.log(cons(3,5));

// self invoking function
(function(a,b){
    console.log("in self invoking function");
    return a+b;
})(2,7);
