import React from 'react';
import { StyleSheet, View } from 'react-native';
import { Card, Text, LineItem } from 'components';
import { scale } from 'react-native-size-matters';
import PropTypes from 'prop-types';
import { getProductById } from 'mocks/products';

const styles = StyleSheet.create({
  card: {
    padding: scale(14),
    marginBottom: scale(14),
  },
  imageContainer: {
    borderRadius: scale(8),
    overflow: 'hidden',
    width: scale(80),
    aspectRatio: 1 / 1,
    marginRight: scale(14),
  },
  image: {
    flex: 1,
    width: undefined,
    height: undefined,
  },
  item: {
    flexDirection: 'row',
    marginTop: scale(14),
  },
  price: {
    alignItems: 'flex-end',
    marginLeft: scale(14),
  },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  breakdown: {
    flexDirection: 'row',
    marginTop: scale(14),
  },
  flex: {
    flex: 1,
  },
});

const Orders = ({ order }) => (
  <Card style={styles.card}>
    <Text weight="medium">{order.shop.name}</Text>
    {order.products.map((product) => {
      const item = getProductById(product.id);
      return (
        <LineItem product={item} key={item.id} />
      );
    })}
    <View style={styles.breakdown}>
      <View style={styles.flex} />
      <View style={styles.flex}>
        <View style={styles.row}>
          <Text>Shipping</Text>
          <Text>{order.shippingFee}</Text>
        </View>
        {order.shopPromo && (
        <View style={styles.row}>
          <Text>Shop Promotion</Text>
          <Text color="primary">{order.shopPromo}</Text>
        </View>
        )}
        <View style={styles.row}>
          <Text>Subtotal</Text>
          <Text weight="medium">{order.subtotal}</Text>
        </View>
      </View>
    </View>
  </Card>
);

Orders.propTypes = {
  order: PropTypes.object.isRequired,
};

export default Orders;
