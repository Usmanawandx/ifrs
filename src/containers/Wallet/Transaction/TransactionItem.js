import React from 'react';
import { StyleSheet, View } from 'react-native';
import {
  Card, Text, Avatar,
} from 'components';
import { scale, verticalScale } from 'react-native-size-matters';
import { getShopById } from 'mocks/shops';
import Colors from 'themes/colors';
import Icon from 'react-native-vector-icons/MaterialCommunityIcons';
import PropTypes from 'prop-types';

const styles = StyleSheet.create({
  container: {
    marginTop: verticalScale(10),
    flexDirection: 'row',
    padding: scale(14),
    alignItems: 'center',
  },
  content: {
    flex: 1,
    paddingHorizontal: scale(14),
  },
  amount: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  deposit: {
    width: scale(40),
    height: scale(40),
    borderRadius: scale(20),
    backgroundColor: Colors.primary,
    justifyContent: 'center',
    alignItems: 'center',
  },
});

const TransactionItem = ({
  description, title, type, shopId, amount,
}) => {
  if (type === 'debit') {
    const shop = getShopById(shopId);
    return (
      <Card style={styles.container}>
        <Avatar source={shop.image} size={40} />
        <View style={styles.content}>
          <Text weight="medium" color="gray75" numberOfLines={1}>{shop.name}</Text>
          <Text color="gray50">{description}</Text>
        </View>
        <Text font="p1" weight="medium" color="tertiary">{`- ${amount}`}</Text>
      </Card>
    );
  }
  return (
    <Card style={styles.container}>
      <View style={styles.deposit}>
        <Icon
          name="wallet-plus-outline"
          color={Colors.white}
          size={scale(20)}
        />
      </View>
      <View style={styles.content}>
        <Text weight="medium" color="gray75">{title}</Text>
        <Text color="gray50">{description}</Text>
      </View>
      <Text font="p1" weight="medium" color="green">{`+ ${amount}`}</Text>
    </Card>
  );
};

TransactionItem.propTypes = {
  description: PropTypes.string.isRequired,
  title: PropTypes.string,
  type: PropTypes.string.isRequired,
  shopId: PropTypes.number,
  amount: PropTypes.number.isRequired,
};

TransactionItem.defaultProps = {
  title: null,
  shopId: null,
};

export default TransactionItem;
