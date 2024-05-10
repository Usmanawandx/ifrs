import React, { useContext, useState } from 'react';
import {
  View, StyleSheet, TouchableOpacity, Image, ScrollView
} from 'react-native';
import {
  Container,
  Button,
  TextField,
  KeyboardAvoidingView,
  Text,
  IconButton,
  Divider,
} from 'components';
import Colors from 'themes/colors';
import PropTypes from 'prop-types';
import { scale } from 'react-native-size-matters';
import { AuthContext } from 'contexts/AuthContext';
import { getScreenWidth } from 'utils/size';
import baseUrl from '../../../assets/common/baseUrl';
import AsyncStorage from '@react-native-community/async-storage';
import axios from 'axios';
import { error } from 'react-native-gifted-chat/lib/utils';

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  header: {
    flex: 0.5,
    alignItems: 'center',
    justifyContent: 'center',
    paddingTop: scale(24),
    backgroundColor: 'white',
    borderRadius: scale(600),
    margin: 10
  },
  form: {
    flex: 1,
    backgroundColor: Colors.white,
    paddingVertical: scale(24),
    paddingHorizontal: scale(14),
    borderTopLeftRadius: scale(20),
    borderTopRightRadius: scale(20),
  },
  welcome: {
    marginBottom: scale(14),
  },
  signUpContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    flex: 1,
  },
  socialContainer: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    marginVertical: scale(14),
    flex: 1,
  },
  social: {
    width: scale(50),
    height: scale(50),
    borderRadius: scale(25),
    backgroundColor: Colors.gray5,
    alignItems: 'center',
    justifyContent: 'center',
    marginHorizontal: scale(5),
  },
  forgot: {
    alignItems: 'flex-end',
    flex: 1,
  },
  logo: {
    flex: 1,
    width: getScreenWidth() / 1.2,
    resizeMode: 'contain',
  },
});
const validationsfields = {
  email: null,
  password: null,
}
const storeData = async (token) => {
  try {
    const jsonValue = (token)

    await AsyncStorage.setItem('@auth_token', jsonValue)
    console.log("saved")
  } catch (e) {
    console.log("error")
    // saving error
  }
}

const Auth = ({ navigation }) => {
  const { dispatch } = useContext(AuthContext);
  const [email, setEmail] = useState('sheikh123@gmail.com');
  const [password, setPassword] = useState('12345');
  const [ec, setEc] = useState(validationsfields);
  // const validateEmail = (email) => {
  //   return String(email)
  //     .toLowerCase()
  //     .match(
  //       /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
  //     );
  // };
  const validateEmail = (email) => {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
  };
  const loginUser = () => {

    var d = { email: null, password: null }
    if (email == undefined || email == null || email == '') {
      d.email = "Please provide email Address";
    }
    else {

      if (!validateEmail(email)) {
        d.email = "email not valid"
      }
    }
    if (password == null || password == undefined || password == '') {
      d.password = "Please provide Passwoed";
    }
    setEc(d)
    if (d.email != null || d.password != null) {
      return
    }



    let user = {
      email: email,
      password: password,
    };
    axios
      .post(`${baseUrl}login`, user)
      .then((res) => {
        // console.log("login sucess",res.data)
        if (res.data.Status.Code == 200) {
          storeData(res.data.token)
          console.log("login sucess", res.data.token)
          dispatch({ type: 'SIGN_IN' })

        }
      })
      .catch((error) => {
        console.log(error)
      });
  };
  const forEmail = (text) => {
    let d = { email: null };
    setEc(ec => ({
      ...ec,
      ...d
    }));
    setEmail(text)
  }
  const forPassword = (text) => {
    let d = { password: null };
    setEc(ec => ({
      ...ec,
      ...d
    }));

    setPassword(text)
  }
  return (
    <Container asGradient>
      <KeyboardAvoidingView contentContainerStyle={styles.container}>
        <View style={styles.header}>
          <Image
            source={require('images/branding/logo_with_title.png')}
            style={styles.logo}
          />
        </View>
        <View style={styles.form}>
          <ScrollView>
            <View style={styles.welcome}>
              <Text font="h2" weight="medium">Welcome back!</Text>
              <Text>Please login with your emaill address and password to continue.</Text>
            </View>
            <TextField label="Email address"
              name={"email"}
              id={"email"}
              error={ec.email}
              isCustom={true}
              customSet={(text) => forEmail(text)} />

            <TextField label="Password" secureTextEntry
              name={"password"}
              id={"password"}
              error={ec.password}
              // error={"password is requird"}
              isCustom={true}
              customSet={(text) => forPassword(text)} />

            <View style={styles.forgot}>
              <TouchableOpacity onPress={() => navigation.navigate('ForgotPassword')}>
                <Text
                  color="primary"
                  weight="medium"
                >
                  Forgot password?
                </Text>
              </TouchableOpacity>
            </View>
            <Button label="Sign In" onPress={() => loginUser()} />
            {/* <Button label="Sign In" onPress={() => dispatch({ type: 'SIGN_IN' })} /> */}

            {/* <Button label="Sign In" onPress={() => loginUser()} /> */}

            <Divider>
              <Text color="gray50">or</Text>
            </Divider>

            <View style={styles.socialContainer}>
              <IconButton
                iconType="MaterialCommunityIcons"
                icon="apple"
                color="gray75"
                style={styles.social}
                size={24}
              />
              <IconButton
                iconType="MaterialCommunityIcons"
                icon="facebook"
                color="blue"
                style={styles.social}
                size={24}
              />
              <IconButton
                iconType="MaterialCommunityIcons"
                icon="google"
                color="tertiary"
                style={styles.social}
                size={24}
              />
            </View>

            <View style={styles.signUpContainer}>
              <Text>{'Don\'t have an account?'}</Text>
              <TouchableOpacity onPress={() => navigation.navigate('SignUp')}>
                <Text weight="medium" color="primary"> Sign up now!</Text>
              </TouchableOpacity>
            </View>
          </ScrollView>
        </View>
      </KeyboardAvoidingView>
    </Container>
  );
};
Auth.propTypes = {
  navigation: PropTypes.object.isRequired,
};

export default Auth;
