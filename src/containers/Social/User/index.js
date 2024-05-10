import React from 'react';
import { FlatList } from 'react-native';
import PropTypes from 'prop-types';
import UserItem from './UserItem';

const UserList = ({ data }) => (
  <FlatList
    data={data}
    keyExtractor={(item) => item?.id?.toString()}
    renderItem={({ item }) => (
      <UserItem {...item} />
    )}
  />
);

UserList.propTypes = {
  data: PropTypes.array.isRequired,
};

export default UserList;
