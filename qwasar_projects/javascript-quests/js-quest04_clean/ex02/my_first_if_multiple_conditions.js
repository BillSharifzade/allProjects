a = 10
b = 9
c = 11
d = 10
y = 9
z = 11

if (a > b && a < c && a === d) {
  console.log("a is bigger than b AND smaller than c AND equal to d");
}
if (z > a || y > a) {
  console.log("z OR y are bigger than a");
}