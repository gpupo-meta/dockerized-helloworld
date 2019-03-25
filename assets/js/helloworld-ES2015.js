let person = {
  name: 'World',
  sayName: function() {
    return this.name;
  }
};

console.log(`Hello ${person.sayName()}`);
