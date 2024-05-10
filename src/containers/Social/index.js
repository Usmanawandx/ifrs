import React from 'react';
import { Container, NavBar, Tabs } from 'components';
import PropTypes from 'prop-types';
import { View, StyleSheet } from 'react-native';
import { getUsers } from 'mocks/users';
import { getShops } from 'mocks/shops';
import { getProfile } from 'mocks/me';
import UserList from './User';

const users = getUsers();
const shops = getShops();
const profile = getProfile();

const styles = StyleSheet.create({
  tab: {
    flex: 1,
  },
});

const Social = ({ navigation }) => (
  <Container backgroundColor="white">
    <NavBar
      title={profile.username}
      onLeftIconPress={() => navigation.goBack()}
    />
    <Tabs>
      <View label="5 following" style={styles.tab}>
        <UserList data={shops} />
      </View>
      <View label="11 followers" style={styles.tab}>
        <UserList data={users} />
      </View>
    </Tabs>
  </Container>
);

Social.propTypes = {
  navigation: PropTypes.object.isRequired,
};

export default Social;
