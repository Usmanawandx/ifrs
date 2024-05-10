import React from 'react';
import {
  StyleSheet, ScrollView, View,
} from 'react-native';
import {
  Text, NavBar, Container, GradientBlock, ProductList,
} from 'components';
import { scale, verticalScale } from 'react-native-size-matters';
import { getCarts } from 'mocks/carts';
import PropTypes from 'prop-types';
import CartList from './List';
import Footer from './Footer';

const styles = StyleSheet.create({
  container: {
    paddingHorizontal: scale(14),
    marginTop: verticalScale(-70),
  },
  block: {
    flexDirection: 'row',
    paddingHorizontal: scale(14),
    paddingBottom: verticalScale(10),
    height: verticalScale(100),
  },
  item: {
    marginRight: scale(20),
  },
});

const Cart = ({ navigation }) => {
  const carts = getCarts();

  return (
    <Container>
      <NavBar title="Shopping Cart" />
      <ScrollView bounces={false}>
        <GradientBlock style={styles.block}>
          <Text color="white" style={styles.item}>5 items</Text>
          <Text color="white" numberOfLines={1} flex>Address: 2757  Browning Lane, 53718 Madison, Wisconsin</Text>
        </GradientBlock>
        <View style={styles.container}>
          <CartList
            carts={carts}
          />
        </View>
        <ProductList
          title="You might also like"
          numberOfProducts={6}
          navigation={navigation}
        />
      </ScrollView>
      <Footer
        subtotal={carts.subtotal}
        navigation={navigation}
      />
    </Container>
  );
};

Cart.propTypes = {
  navigation: PropTypes.object.isRequired,
};

export default Cart;
