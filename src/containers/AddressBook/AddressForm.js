import React from 'react';
import { StyleSheet, View, SafeAreaView } from 'react-native';
import {
  TextField,
  KeyboardAvoidingView,
  Switch,
  Text,
  Button,
} from 'components';
import PropTypes from 'prop-types';
import { scale } from 'react-native-size-matters';

const styles = StyleSheet.create({
  container: {
    padding: scale(14),
  },
  flex: {
    flex: 1,
  },
  buttonContainer: {
    padding: scale(14),
  },
  divider: {
    marginHorizontal: scale(7),
  },
  switch: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  row: {
    flexDirection: 'row',
    flex: 1,
  },
});

const AddressForm = ({
  name, phoneNumber, street, postalCode, city, state, isDefault,
}) => (
  <SafeAreaView style={styles.flex}>
    <KeyboardAvoidingView contentContainerStyle={styles.container}>
      <TextField label="Receiver Name" initialValue={name} />
      <TextField label="Phone Number" initialValue={phoneNumber} />
      <TextField label="Street" initialValue={street} />
      <View style={styles.row}>
        <View style={styles.flex}>
          <TextField label="Postal Code" initialValue={postalCode} />
        </View>
        <View style={styles.divider} />
        <View style={styles.flex}>
          <TextField label="City" initialValue={city} />
        </View>
      </View>
      <TextField label="State" initialValue={state} />
      <View style={styles.switch}>
        <Text>Set as default</Text>
        <Switch initialValue={isDefault} />
      </View>
    </KeyboardAvoidingView>
    <View style={styles.buttonContainer}>
      <Button label="Save" />
    </View>
  </SafeAreaView>
);

AddressForm.propTypes = {
  name: PropTypes.string,
  phoneNumber: PropTypes.string,
  street: PropTypes.string,
  city: PropTypes.string,
  postalCode: PropTypes.string,
  state: PropTypes.string,
  isDefault: PropTypes.bool,
};

AddressForm.defaultProps = {
  name: '',
  phoneNumber: '',
  street: '',
  city: '',
  postalCode: '',
  state: '',
  isDefault: false,
};

export default AddressForm;
