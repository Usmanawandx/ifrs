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

const NewAddress = ({ navigation, route }) => {
  const { initialValues } = route?.params;

  return (
    <Container
      style={styles.outer}
    >
      <NavBar
        title="New Address"
        onLeftIconPress={() => navigation.goBack()}
      />
      <AddressForm {...initialValues} />
    </Container>
  );
};

NewAddress.propTypes = {
  navigation: PropTypes.object.isRequired,
  route: PropTypes.object.isRequired,
};

export default NewAddress;
