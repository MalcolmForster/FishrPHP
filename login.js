//need to 'require' stuff https://codeshack.io/basic-login-system-nodejs-express-mysql/
/* ------------ TO DO LIST -----------------------


*/

//const mysql = require('mysql');
const loginForm = document.getElementById("login");
const loginButton = document.getElementById("login-form-submit");
const loginErrorMsg = document.getElementById("login-error-msg");


if (e != null) {
    loginButton.addEventListener("click", e => {
    e.preventDefault();
    const username = login.Uname.value;
    const password = login.pass.value;

    if (username === "asdf" && password === "asdf") {
        alert("You have successfully logged in.");
        location.reload();
    } else {
        loginErrorMsg.style.opacity = 1;
    }

});
}
