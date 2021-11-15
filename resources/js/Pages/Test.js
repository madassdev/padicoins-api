import axios from "axios";
import React from "react";

function Test() {
    fetch("https://padicoins-test.herokuapp.com/api/wallets", {
        method: "post",
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
      
        //make sure to serialize your JSON body
        body: JSON.stringify({
          name: "Ola",
          password: "pass"
        })
      })
      .then( (response) => { 
         //do something awesome that makes the world a better place
         console.log(response)
      });
      
    return <div>Test</div>;
}

export default Test;
