import React from 'react';
import { createStackNavigator } from '@react-navigation/stack';
import Feed from 'containers/Feed';
import FeedComments from 'containers/Feed/Comments';

const Stack = createStackNavigator();

function FeedStack() {
  return (
    <Stack.Navigator headerMode="none">
      <Stack.Screen name="Feed" component={Feed} />
      <Stack.Screen name="FeedComments" component={FeedComments} />
    </Stack.Navigator>
  );
}

export default FeedStack;
