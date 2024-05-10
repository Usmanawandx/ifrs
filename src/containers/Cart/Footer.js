import React from 'react';
import { View, StyleSheet } from 'react-native';
import { Text, Button, Checkbox } from 'components';
import Colors from 'themes/colors';
import { scale } from 'react-native-size-matters';
import PropTypes from 'prop-types';

const styles = StyleSheet.create({
  container: {
    backgroundColor: Colors.white,
    paddingHorizontal: scale(24),
    paddingVertical: scale(10),
    flexDirection: 'row',
    alignItems: 'center',
    borderTopWidth: 1,
    borderColor: Colors.gray10,
  },
  all: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  checkbox: {
    marginRight: scale(5),
  },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  text: {
    marginRight: scale(10),
  },
});

const Footer = ({ subtotal, navigation }) => (
  <View style={styles.container}>
    <View style={styles.all}>
      <Checkbox style={styles.checkbox} label="All" />
    </View>
    <View style={styles.row}>
      <View style={styles.text}>
        <View style={styles.row}>
          <Text>Subtotal: </Text>
          <Text
            font="p1"
            weight="medium"
            color="tertiary"
          >
            {subtotal}
          </Text>
        </View>
        <Text color="tertiary" font="h5">Earn 130 Coins</Text>
      </View>
      <Button
        label="Checkout (5)"
        onPress={() => navigation.navigate('Checkout')}
      />
    </View>
  </View>
);

Footer.propTypes = {
  subtotal: PropTypes.string.isRequired,
  navigation: PropTypes.object.isRequired,
};

export default Footer;
