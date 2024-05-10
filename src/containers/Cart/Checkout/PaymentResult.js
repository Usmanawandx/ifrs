import React from 'react';
import {
  Container, Text, Button, Coin,
} from 'components';
import {
  StyleSheet, View, SafeAreaView, StatusBar,
} from 'react-native';
import Colors from 'themes/colors';
import Icon from 'react-native-vector-icons/MaterialCommunityIcons';
import { scale } from 'react-native-size-matters';
import PropTypes from 'prop-types';
import sample from 'lodash/sample';

const styles = StyleSheet.create({
  container: {
    backgroundColor: Colors.white,
    padding: scale(14),
  },
  header: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: scale(14),
  },
  buttonContainer: {
    paddingVertical: scale(10),
  },
  flex: {
    flex: 1,
  },
  detail: {
    flex: 1,
  },
  line: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: scale(14),
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderColor: Colors.gray25,
  },
  coin: {
    marginRight: scale(10),
  },
  row: {
    flexDirection: 'row',
    flex: 1,
    alignItems: 'center',
  },
  headerText: {
    alignItems: 'center',
    paddingVertical: scale(14),
  },
  subtitle: {
    marginVertical: scale(10),
  },
});

const Line = ({ label, value, showCoin }) => (
  <View style={styles.line}>
    <Text flex color="gray50">{label}</Text>
    <View style={styles.row}>
      {showCoin && (
      <Coin color="primary" size={16} style={styles.coin} />
      )}
      <Text
        weight="medium"
      >
        {value}
      </Text>
    </View>
  </View>
);

const PaymentResult = ({ navigation }) => {
  const result = sample([true, false]);

  const renderSuccess = () => (
    <>
      <View style={styles.header}>
        <Icon
          size={scale(100)}
          name="check-circle"
          color={Colors.green}
        />
        <View style={styles.headerText}>
          <Text font="h2" weight="medium">Order placed successfully</Text>
          <Text
            color="gray75"
            centered
            style={styles.subtitle}
          >
            We have sent you the receipt to your mailbox.
          </Text>
        </View>
      </View>
      <View style={styles.detail}>
        <Line label="Order ID" value="ECP284849839" />
        <Line label="Amount Paid" value="$638.00" />
        <Line label="Payment ID" value="284040382019" />
        <Line label="Coins Reward" value="600" showCoin />
      </View>
    </>
  );

  const renderFail = () => (
    <>
      <View style={styles.header}>
        <Icon
          size={scale(100)}
          name="close-circle"
          color={Colors.tertiary}
        />
        <View style={styles.headerText}>
          <Text font="h2" weight="medium">Payment failed</Text>
          <Text
            color="gray75"
            centered
            style={styles.subtitle}
          >
            Please check your internet connection and try again later.
          </Text>
        </View>
      </View>
      <View style={styles.detail}>
        <Line label="Error Code" value="E2003" />
        <Line label="Payment ID" value="284040382019" />
      </View>
    </>
  );

  return (
    <Container style={styles.container}>
      <StatusBar barStyle="dark-content" />
      <SafeAreaView style={styles.flex}>
        {result && renderSuccess()}
        {!result && renderFail()}
        <View style={styles.buttonContainer}>
          <Button
            label="Done"
            onPress={() => navigation.navigate('Home')}
          />
        </View>
      </SafeAreaView>
    </Container>
  );
};

PaymentResult.propTypes = {
  navigation: PropTypes.object.isRequired,
};

Line.propTypes = {
  label: PropTypes.string.isRequired,
  value: PropTypes.string.isRequired,
  showCoin: PropTypes.bool,
};

Line.defaultProps = {
  showCoin: false,
};

export default PaymentResult;
