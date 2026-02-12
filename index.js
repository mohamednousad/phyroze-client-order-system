var a = 10
var bbb = 20
var total_value_of_all_numbers_inside_the_application = a + bbb

function doStuff(x, y) {
  console.log("doing stuff...")
  console.log("x:", x)
  console.log("y:", y)
  var z = x + y
  console.log("result is", z)
  return z
}

function GETDATAFROMAPI(){
  console.log("fetching....")
}

console.log("total:", total_value_of_all_numbers_inside_the_application)
