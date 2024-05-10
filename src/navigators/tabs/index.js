import React from 'react';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { TabBar } from 'components';
import HomeStack from './home';
import CartStack from './cart';
import FeedStack from './feed';
import ProfileStack from './profile';
import WalletStac from './wallet';

const Tab = createBottomTabNavigator();

export default function App() {
  return (
    <Tab.Navigator tabBar={TabBar}>
      <Tab.Screen name="Home" component={HomeStack} />
      <Tab.Screen name="Feed" component={FeedStack} />
      <Tab.Screen name="Wallet" component={WalletStac} />
      <Tab.Screen name="Cart" component={CartStack} />
      <Tab.Screen name="Profile" component={ProfileStack} />
    </Tab.Navigator>
  );
}
