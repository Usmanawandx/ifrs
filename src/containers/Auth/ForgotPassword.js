import React, { useState } from 'react';
import {
  Container, NavBar, Divider, TextField, Text,
} from 'components';
import { StyleSheet } from 'react-native';
import PropTypes from 'prop-types';
import { scale } from 'react-native-size-matters';
import FormContaienr from './FormContainer';
// import Toast from "react-native-toast-message";
import Toast from 'react-native-tiny-toast'
import axios from 'axios';
import baseUrl from '../../../assets/common/baseUrl';

const styles = StyleSheet.create({
  divider: {
    marginBottom: scale(30),
  },
});
const validationsfields={
  email:null
}

const ForgotPassword = ({ navigation }) => {
  const [email , setEmail] = useState("");
  const [error , setError] = useState("");

  const validateEmail = (email) => {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
  };

  const forgetUserPassword =() =>{
    var x={email:null}
    if (email ==undefined || email == null || email == '' ) {
     x.email="Please provide email Address";
   }
   else{

     if(!validateEmail(email)){
       x.email="email not valid"
     }
   }
   setError(x)
   if(x.email !=null){
       return
   }
let password={
  email: email
};
axios
.post(`${baseUrl}reset`,password)
.then((res)=>{
  if(res.data.Status.Code == 200){
    Toast.showSuccess(res.data.Status.SuccessMessage)
    console.log("email send" ,res.data.Status.SuccessMessage)
    
  }
})
.catch((error)=>{
  Toast.show({
    topOffset: 60,
    type: "error",
    text1: res.data.Status.ErrorMessage ,
  });
  console.log("email not send" ,res.data.Status.ErrorMessage )
})

  }
  const forEmail=(text)=>{
    let x={email:null};
    setError(error=>({
      ...ec,
      ...x
    }));
    setEmail(text)
  }
  return(
  <Container asGradient>
    <NavBar
      onLeftIconPress={() => navigation.goBack()}
    />
    <FormContaienr
      title="Forgot your password?"
      subtitle="We got your back! Let us know your email or phone number and we will send a 6-digits PIN for verification to reset your password."
      buttonLabel="Continue"
      onSubmit={() => forgetUserPassword() }
    >
      <TextField 
      label="Email address"
      name={"email"}
      id={"email"}
      error={error.email}
       isCustom={true}
      customSet={(text) => forEmail(text)}
      />
     
    </FormContaienr>
  </Container>
)};

ForgotPassword.propTypes = {
  navigation: PropTypes.object.isRequired,
};

export default ForgotPassword;
