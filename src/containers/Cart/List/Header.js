import React from 'react';
import { StyleSheet, View, TouchableOpacity } from 'react-native';
import { Text, Avatar, Checkbox } from 'components';
import { scale } from 'react-native-size-matters';
import PropTypes from 'prop-types';
import Icon from 'react-native-vector-icons/MaterialCommunityIcons';
import Colors from 'themes/colors';

const styles = StyleSheet.create({
  container: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  content: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  avatar: {
    marginHorizontal: scale(10),
  },
});

const Header = ({ shop }) => (
  <TouchableOpacity style={styles.container}>
    <View style={styles.content}>
      <Checkbox />
      <Avatar source={shop.image} size={30} style={styles.avatar} />
      <Text
        numberOfLines={1}
        weight="medium"
        style={styles.name}
      >
        {shop.name}
      </Text>
    </View>
    <Icon
      name="chevron-right"
      size={scale(20)}
      color={Colors.gray50}
    />
  </TouchableOpacity>
);

Header.propTypes = {
  shop: PropTypes.object.isRequired,
};

export default Header;
