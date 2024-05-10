import React, { useRef, useState } from 'react';
import { StyleSheet, View, TouchableOpacity } from 'react-native';
import {
  Text, Card, GradientIcon, BottomSheet, PaymentSelect,
} from 'components';
import { scale } from 'react-native-size-matters';
import Icon from 'react-native-vector-icons/MaterialCommunityIcons';
import Colors from 'themes/colors';
import { getCarts } from 'mocks/carts';
import { getProfile } from 'mocks/me';
import { getScreenHeight } from 'utils/size';
import isEmpty from 'lodash/isEmpty';

const styles = StyleSheet.create({
  card: {
    padding: scale(14),
    marginBottom: scale(14),
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  content: {
    marginHorizontal: scale(14),
    flex: 1,
  },
  payment: {
    marginTop: scale(14),
  },
  divider: {
    borderWidth: StyleSheet.hairlineWidth,
    marginVertical: scale(14),
    borderColor: Colors.gray25,
    borderStyle: 'dashed',
  },
  method: {
    borderWidth: StyleSheet.hairlineWidth,
    borderColor: Colors.blue,
    borderRadius: scale(8),
    marginTop: scale(14),
    flexDirection: 'row',
    alignItems: 'center',
    padding: scale(14),
    backgroundColor: Colors.blueAlt,
  },
  sheet: {
    padding: scale(14),
    flex: 1,
  },
  select: {
    marginBottom: scale(14),
  },
});

const profile = getProfile();

const PaymentCard = () => {
  const { subtotal, total, shippingFee } = getCarts();
  const [payment, setPayment] = useState({});
  const sheetRef = useRef(null);

  return (
    <Card style={styles.card}>
      <View style={styles.header}>
        <GradientIcon
          icon="currency-usd"
          size={18}
        />
        <View style={styles.content}>
          <Text>Payment Method</Text>
        </View>
        <View style={styles.row}>
          <TouchableOpacity onPress={() => sheetRef.current.open()}>
            <Text color="gray50">Select</Text>
          </TouchableOpacity>
          <Icon
            name="chevron-right"
            color={Colors.gray25}
            size={scale(20)}
          />
        </View>
      </View>
      <TouchableOpacity style={styles.method} onPress={() => sheetRef.current.open()}>
        {isEmpty(payment)
          ? <Text color="blue">Please select a payment method</Text>
          : (
            <>
              {payment.Icon}
              <View style={styles.content}>
                <Text weight="medium">{payment.title}</Text>
                <Text color="gray75">{payment.subtitle}</Text>
              </View>
              <Icon
                name="check-circle"
                size={scale(20)}
                color={Colors.blue}
              />
            </>
          )}
      </TouchableOpacity>
      <View style={styles.payment}>
        <View style={styles.row}>
          <Text>Merchandise Subtotal</Text>
          <Text>{subtotal}</Text>
        </View>
        <View style={styles.row}>
          <Text>Shipping Subtotal</Text>
          <Text>{shippingFee}</Text>
        </View>
        <View style={styles.divider} />
        <View style={styles.row}>
          <Text weight="medium">Total Payment</Text>
          <Text weight="medium" color="primary" font="p1">{total}</Text>
        </View>
      </View>
      <BottomSheet sheetRef={sheetRef} buttonText="Confirm" height={getScreenHeight() / 1.5}>
        <View style={styles.sheet}>
          <Text weight="medium" style={styles.select}>Select payment:</Text>
          <PaymentSelect
            onSelect={setPayment}
            options={[
              ...profile.cards,
              {
                title: 'PayPal',
                subtitle: 'Redirect to PayPal',
                icon: 'paypal',
                iconColor: Colors.blue,
              },
              {
                title: 'Online Banking',
                subtitle: 'Internet banking log-in needed',
                icon: 'university',
                iconColor: Colors.tertiary,
              },
            ]}
          />
        </View>
      </BottomSheet>
    </Card>
  );
};

export default PaymentCard;
