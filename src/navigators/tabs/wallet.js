import React from 'react';
import { createStackNavigator } from '@react-navigation/stack';
import Wallet from 'containers/Wallet';
import TopUp from 'containers/Wallet/TopUp';
import AllTransactions from 'containers/Wallet/Transaction';

const Stack = createStackNavigator();

function WalletStack() {
  return (
    <Stack.Navigator headerMode="none">
      <Stack.Screen name="Wallet" component={Wallet} />
      <Stack.Screen name="TopUp" component={TopUp} />
      <Stack.Screen name="AllTransactions" component={AllTransactions} />
    </Stack.Navigator>
  );
}

export default WalletStack;
