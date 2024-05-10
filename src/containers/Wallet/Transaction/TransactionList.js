import React from 'react';
import {
  SectionList, View, StyleSheet,
} from 'react-native';
import PropTypes from 'prop-types';
import { Text } from 'components';
import { verticalScale } from 'react-native-size-matters';
import TransactionItem from './TransactionItem';

const styles = StyleSheet.create({
  container: {
  },
  section: {
    marginTop: verticalScale(20),
  },
});

const TransactionList = ({ transactions, ListHeaderComponent, style }) => (
  <SectionList
    stickySectionHeadersEnabled={false}
    style={[styles.container, style]}
    sections={transactions}
    keyExtractor={(data) => data.id}
    renderSectionHeader={({ section: { section } }) => (
      <View style={styles.section}>
        <Text font="p1" color="gray50" weight="medium">{section}</Text>
      </View>
    )}
    renderItem={({ item }) => (
      <TransactionItem {...item} />
    )}
    ListHeaderComponent={ListHeaderComponent}
  />
);

TransactionList.propTypes = {
  transactions: PropTypes.array.isRequired,
  ListHeaderComponent: PropTypes.node,
  style: PropTypes.any,
};

TransactionList.defaultProps = {
  ListHeaderComponent: null,
  style: null,
};

export default TransactionList;
