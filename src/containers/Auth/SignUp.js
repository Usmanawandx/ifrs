import React, { useContext, useState } from 'react';
import {
  Container, NavBar, TextField,
} from 'components';
import PropTypes from 'prop-types';
// import { AuthContext } from 'contexts/AuthContext';
import FormContainer from './FormContainer';
import { ScrollView } from 'react-native';
import baseUrl from '../../../assets/common/baseUrl';
import Toast from 'react-native-tiny-toast';
import axios from 'axios';

const validationsfields = {
  email: null,
  name: null,
  phone: null,
  email: null,
  address: null,
  password: null,
  c_password: null,
}

const SignUp = ({ navigation }) => {
  const [email, setEmail] = useState("");
  const [name, setName] = useState("");
  const [phone, setPhone] = useState("");
  const [address, setAddress] = useState("");
  const [password, setPassword] = useState("");
  const [c_password, setC_password] = useState("");
  const [error, setError] = useState(validationsfields);

  const validateEmail = (email) => {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
  };
  
  const signUp = () => {
    var x = { email: null, name: null, phone: null, address: null, password: null, c_password: null }
    if (email == undefined || email == null || email == '') {
      x.email = "Please provide Email address";
    }
    else{

      if(!validateEmail(email)){
        x.email="email not valid"
      }
    }
    if (name == undefined || name == null || name == '') {
      x.name = "Please provide name";
    }
    if (phone == undefined || phone == null || phone == '') {
      x.phone = "Please provide phone number";
    }
    if (address == undefined || address == null || address == '') {
      x.address = "Please provide address";
    }
    if (password == undefined || password == null || password == '') {
      x.password = "Please provide Password";
    }
    if (c_password == undefined || c_password == null || c_password == '') {
      x.c_password = "Please provide Confirm Password";
    }

    setError(x)
    if (x.email != null || x.name != null || x.phone != null || x.address != null || x.password != null || x.c_password != null) {
      return
    }

    let user = {
      name: name,
      email: email,
      address: address,
      password: password,
      c_password: c_password,
      phone: phone,
    };
    axios
      .post(`${baseUrl}register`, user)
      .then((res) => {
        if (res.data.Status.Code == 200) {
          Toast.showSuccess(res.data.Status.SuccessMessage)
          console.log("sign in done")
          setTimeout(() => {
            navigation.navigate("SignIn");
          }, 500);
        }
        else if (res.data.Status.Code == 401) {
          Toast.showSuccess(res.data.Status.ErrorMessage.name)
          console.log("sign in error")
        }
      })
      .catch((error) => {
        console.log(error.res.data.ErrorMessage)
      });
  };
  const forEmail = (text) => {
    let x = { email: null };
    setError(error => ({
      ...error,
      ...x
    }));
    setEmail(text)
  }

  const forphone = (text) => {
    let x = { phone: null };
    setError(error => ({
      ...error,
      ...x
    }));
    setPhone(text)
  }

  const forName = (text) => {
    let x = { name: null };
    setError(error => ({
      ...error,
      ...x
    }));
    setName(text)
  }

  const forAddress = (text) => {
    let x = { address: null };
    setError(error => ({
      ...error,
      ...x
    }));
    setAddress(text)
  }

  const forPassword = (text) => {
    let x = { password: null };
    setError(error => ({
      ...error,
      ...x
    }));
    setPassword(text)
  }

  const forC_password = (text) => {
    let x = { c_password: null };
    setError(error => ({
      ...error,
      ...x
    }));
    setC_password(text)
  }

  return (
    <Container asGradient>
      <NavBar
        onLeftIconPress={() => navigation.goBack()}
      />
      <FormContainer
        title="Create an Account"
        subtitle="Join today along with million other users to the most exclusive e-commerce platform ever!"
        buttonLabel="Sign Up"
        onSubmit={() => signUp()}
      >
        <ScrollView>
          <TextField label="Name"
            name={"name"}
            id={"name"}
            error={error.name}
            isCustom={true}
            customSet={(text) => forName(text)}
          // customSet={(text) => setName(text)} 
          />

          <TextField label="Phone number"
            name={"phone"}
            id={"phone"}
            error={error.phone}
            isCustom={true}
            customSet={(text) => forphone(text)}
            keyboardType={"numeric"} />

          <TextField label="Email address"
            name={"email"}
            id={"email"}
            error={error.email}
            isCustom={true}
            customSet={(text) => forEmail(text)} />

          <TextField label="Address"
            name={"address"}
            id={"address"}
            error={error.address}
            isCustom={true}
            customSet={(text) => forAddress(text)} />

          <TextField label="Password" secureTextEntry
            name={"password"}
            id={"password"}
            error={error.password}
            isCustom={true}
            customSet={(text) => forPassword(text)} />

          <TextField label="Confirm Password" secureTextEntry
            name={"c_password"}
            id={"c_password"}
            error={error.c_password}
            isCustom={true}
            customSet={(text) => forC_password(text)} />
        </ScrollView>
      </FormContainer>
    </Container>
  );
};

SignUp.propTypes = {
  navigation: PropTypes.object.isRequired,
};

export default SignUp;
