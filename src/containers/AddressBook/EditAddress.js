import React from 'react';
import { StyleSheet } from 'react-native';
import {
  Container,
  NavBar,
} from 'components';
import PropTypes from 'prop-types';
import AddressForm from './AddressForm';

const styles = StyleSheet.create({
  outer: {
    backgroundColor: 'white',
  },
});

const EditAddress = ({ navigation, route }) => {
  const { initialValues } = route?.params;

  return (
    <Container
      style={styles.outer}
    >
      <NavBar
        title="Edit Address"
        onLeftIconPress={() => navigation.goBack()}
      />
      <AddressForm {...initialValues} />
    </Container>
  );
};

EditAddress.propTypes = {
  navigation: PropTypes.object.isRequired,
  route: PropTypes.object.isRequired,
};

export default EditAddress;
