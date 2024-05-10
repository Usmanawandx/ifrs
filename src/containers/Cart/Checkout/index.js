import React from 'react';
import { StyleSheet } from 'react-native';
import {
  Container, NavBar, KeyboardAvoidingView,
} from 'components';
import PropTypes from 'prop-types';
import { scale } from 'react-native-size-matters';
import { getCarts } from 'mocks/carts';
import AddressCard from './Card/Address';
import PaymentCard from './Card/Payment';
import PromoCard from './Card/Promo';
import OrdersCard from './Card/Orders';
import Footer from './Footer';

const styles = StyleSheet.create({
  container: {
    padding: scale(14),
  },
});

const Checkout = ({ navigation }) => {
  const { items, total } = getCarts();
  return (
    <Container>
      <NavBar
        title="Checkout"
        onLeftIconPress={() => navigation.goBack()}
      />
      <KeyboardAvoidingView contentContainerStyle={styles.container}>
        <AddressCard navigation={navigation} />
        {items.map((item) => (
          <OrdersCard key={item.id} order={item} />
        ))}
        <PromoCard />
        <PaymentCard />
      </KeyboardAvoidingView>
      <Footer total={total} navigation={navigation} />
    </Container>
  );
};
Checkout.propTypes = {
  navigation: PropTypes.object.isRequired,
};

export default Checkout;
