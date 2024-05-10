import React from 'react';
import { View, StyleSheet, SafeAreaView } from 'react-native';
import { Button, Text } from 'components';
import Colors from 'themes/colors';
import { scale } from 'react-native-size-matters';
import PropTypes from 'prop-types';

const styles = StyleSheet.create({
  safeArea: {
    backgroundColor: Colors.white,
    borderTopWidth: 1,
    borderColor: Colors.gray10,
  },
  container: {
    paddingHorizontal: scale(14),
    paddingVertical: scale(10),
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  price: {
    alignItems: 'flex-end',
    marginRight: scale(14),
    flex: 1,
  },
  button: {
    flex: 1,
  },
});

const Footer = ({ total, navigation }) => (
  <SafeAreaView style={styles.safeArea}>
    <View style={styles.container}>
      <View style={styles.price}>
        <Text weight="medium" color="gray75">Total Payment</Text>
        <Text color="primary" font="h2" weight="medium">{total}</Text>
      </View>
      <View style={styles.button}>
        <Button
          onPress={() => navigation.navigate('PaymentResult')}
          label={`Pay ${total}`}
        />
      </View>
    </View>
  </SafeAreaView>
);

Footer.propTypes = {
  total: PropTypes.string.isRequired,
  navigation: PropTypes.object.isRequired,
};

export default Footer;
