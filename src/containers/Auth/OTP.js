import React from 'react';
import {
  Container, NavBar, PIN,
} from 'components';
import { StyleSheet, View } from 'react-native';
import PropTypes from 'prop-types';
import FormContaienr from './FormContainer';

const styles = StyleSheet.create({
  form: {
    flex: 1,
    alignItems: 'center',
  },
});

const OTP = ({ navigation }) => (
  <Container asGradient>
    <NavBar
      onLeftIconPress={() => navigation.goBack()}
    />
    <FormContaienr
      title="OTP Verification"
      subtitle="We have sent a 6-digits PIN to your phone number for verifcaiton purposes."
      buttonLabel="Continue"
      onSubmit={() => navigation.navigate('ResetPassword')}
    >
      <View style={styles.form}>
        <PIN />
      </View>
    </FormContaienr>
  </Container>
);

OTP.propTypes = {
  navigation: PropTypes.object.isRequired,
};

export default OTP;
