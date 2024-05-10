import React from 'react';
import { Container, NavBar } from 'components';
import { View, StyleSheet } from 'react-native';
import PropTypes from 'prop-types';
import { getTransactions } from 'mocks/transactions';
import { scale } from 'react-native-size-matters';
import TransactionList from './TransactionList';

const styles = StyleSheet.create({
  container: {
    paddingHorizontal: scale(14),
    flexGrow: 1,
  },
  flex: {
    flex: 1,
  },
});

const Transactions = ({ navigation }) => (
  <Container>
    <NavBar
      title="All Transactions"
      onLeftIconPress={() => navigation.goBack()}
    />
    <View style={styles.flex}>
      <TransactionList
        style={styles.container}
        transactions={getTransactions()}
      />
    </View>
  </Container>
);

Transactions.propTypes = {
  navigation: PropTypes.object.isRequired,
};

export default Transactions;
