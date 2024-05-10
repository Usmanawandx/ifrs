import React from 'react';
import { ScrollView, StyleSheet } from 'react-native';
import { Container, NavBar, IconButton } from 'components';
import PropTypes from 'prop-types';
import { getProfile } from 'mocks/me';
import { scale } from 'react-native-size-matters';
import AddressCard from './AddressCard';

const styles = StyleSheet.create({
  container: {
    padding: scale(14),
  },
});

const AddressBook = ({ navigation }) => {
  const { addresses } = getProfile();
  return (
    <Container>
      <NavBar
        title="Address Book"
        onLeftIconPress={() => navigation.goBack()}
        renderRightComponent={() => (
          <IconButton
            icon="plus"
            color="white"
            size={22}
            onPress={() => navigation.navigate('NewAddress', { initialValues: {} })}
          />
        )}
      />
      <ScrollView contentContainerStyle={styles.container}>
        {addresses.map((address) => (
          <AddressCard
            key={address.type}
            address={address}
            navigation={navigation}
          />
        ))}
      </ScrollView>
    </Container>
  );
};
AddressBook.propTypes = {
  navigation: PropTypes.object.isRequired,
};

export default AddressBook;
