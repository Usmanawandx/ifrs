import React from 'react';
import { createStackNavigator } from '@react-navigation/stack';
import Cart from 'containers/Cart';

const Stack = createStackNavigator();

function CartStack() {
  return (
    <Stack.Navigator headerMode="none">
      <Stack.Screen name="Cart" component={Cart} />
    </Stack.Navigator>
  );
}

export default CartStack;
