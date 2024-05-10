import React from 'react';
import { View, StyleSheet } from 'react-native';
import { Avatar, Text, Button } from 'components';
import PropTypes from 'prop-types';
import { scale } from 'react-native-size-matters';
import Colors from 'themes/colors';

const styles = StyleSheet.create({
  container: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: Colors.white,
    borderBottomWidth: 1,
    borderColor: Colors.gray10,
    padding: scale(14),
  },
  content: {
    marginHorizontal: scale(14),
    flex: 1,
  },
});

const UserItem = ({ image, name, username }) => (
  <View style={styles.container}>
    <Avatar source={image} size={50} />
    <View style={styles.content}>
      <Text weight="medium" numberOfLines={1}>{name}</Text>
      <Text color="gray75" numberOfLines={1}>{username}</Text>
    </View>
    <Button size="tiny" label="Following" variant="ghost" />
  </View>
);

UserItem.propTypes = {
  image: PropTypes.any.isRequired,
  name: PropTypes.string.isRequired,
  username: PropTypes.string.isRequired,
};

export default UserItem;
