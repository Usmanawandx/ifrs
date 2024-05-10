import React from 'react';
import {
  NavBar,
  Container,
  Text,
  GradientBlock,
  Coin,
  Card,
  IconButton,
  GradientIcon,
} from 'components';
import {
  StyleSheet, View, ScrollView, TouchableOpacity,
} from 'react-native';
import { verticalScale, scale } from 'react-native-size-matters';
import { getTransactions } from 'mocks/transactions';
import PropTypes from 'prop-types';
import TransactionList from './Transaction/TransactionList';

const styles = StyleSheet.create({
  header: {
    height: verticalScale(120),
    alignItems: 'center',
  },
  balance: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  amount: {
    marginLeft: scale(10),
  },
  card: {
    height: verticalScale(80),
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  container: {
    marginTop: verticalScale(-40),
    paddingHorizontal: scale(14),
  },
  button: {
    justifyContent: 'center',
    flex: 1,
    alignItems: 'center',
  },
  text: {
    marginTop: verticalScale(4),
  },
  listHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginTop: verticalScale(14),
  },
  list: {
    paddingBottom: scale(14),
  },
});

const Wallet = ({ navigation }) => (
  <Container>
    <NavBar
      title="Wallet"
      renderRightComponent={() => (
        <IconButton
          icon="credit-card"
          size={22}
          color="white"
          onPress={() => navigation.navigate('PaymentMethod')}
        />
      )}
    />
    <ScrollView bounces={false}>
      <GradientBlock style={styles.header}>
        <Text color="white">COIN BALANCE</Text>
        <View style={styles.balance}>
          <Coin />
          <Text
            color="white"
            font="h1"
            weight="medium"
            style={styles.amount}
          >
            9,000
          </Text>
        </View>
      </GradientBlock>

      <View style={styles.container}>
        <Card style={styles.card}>
          <TouchableOpacity
            style={styles.button}
            onPress={() => navigation.navigate('TopUp')}
          >
            <GradientIcon
              icon="wallet-plus-outline"
              size={40}
            />
            <Text color="gray75" style={styles.text}>Top Up</Text>
          </TouchableOpacity>
          <TouchableOpacity style={styles.button}>
            <GradientIcon
              icon="barcode-scan"
              size={40}
            />
            <Text color="gray75" style={styles.text}>Scan</Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={styles.button}
            onPress={() => navigation.navigate('Pay')}
          >
            <GradientIcon
              icon="qrcode"
              size={40}
            />
            <Text color="gray75" style={styles.text}>Pay</Text>
          </TouchableOpacity>
        </Card>
        <View style={styles.list}>
          <TransactionList
            transactions={getTransactions()}
            ListHeaderComponent={(
              <View style={styles.listHeader}>
                <Text weight="medium" font="h2" color="gray75">Recent Transactions</Text>
                <TouchableOpacity onPress={() => navigation.navigate('AllTransactions')}>
                  <Text color="gray50">View all</Text>
                </TouchableOpacity>
              </View>
            )}
          />
        </View>
      </View>
    </ScrollView>
  </Container>
);

Wallet.propTypes = {
  navigation: PropTypes.object.isRequired,
};

export default Wallet;
